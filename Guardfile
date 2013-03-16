# coding: utf-8

guard 'shell' do
  watch(%r{^factory_girl/(.*)\.php$}){|m|
    filename = "tests/#{m[1]}Test.php"
    if FileTest.exist?(filename)
      run_phpunit(filename)
    else
      puts "\n>> Not found #{filename}\n"
    end
  }
  watch(%r{^tests/(.*)Test\.php$}) {|m| run_phpunit(m[0]) }
end

def run_phpunit(filename)
  puts "\n>> run: #{filename} @ #{Time.now.strftime("%Y-%m-%d %H:%M:%S")}"
  result = `./phpunit --bootstrap tests/bootstrap.php --verbose #{filename}`
  filename = File.basename(filename)
  if result =~ /OK/
    result =~ /(Tests:.+Assertions:.+|\(.+tests.+assertions.?\))/
    success = $+
    n "#{filename}\n#{success}", 'Test passed'
    puts ">> Passed\n" + result
  else
    result =~ /(Tests:.+Assertions:.+Failures:.+)/
    failure = $+
    n "#{filename}\n#{failure}", 'Test failed'
    puts ">> Failed\n" + result
  end
end